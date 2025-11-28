<template>
    <AdminLayout>
        <Head title="Edit Directory" />

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <Link
                            :href="route('admin.directories.index')"
                            class="text-gray-600 hover:text-gray-900 mr-4"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                        </Link>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Edit Directory</h2>
                            <p class="mt-1 text-sm text-gray-600">Update directory information</p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        
                        <div class="space-y-4">
                            <!-- Category & Country -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="directory_category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Category <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="directory_category_id"
                                        v-model="form.directory_category_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-500': form.errors.directory_category_id }"
                                        required
                                    >
                                        <option value="">Select Category</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                            {{ cat.name }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.directory_category_id" class="mt-1 text-sm text-red-600">{{ form.errors.directory_category_id }}</p>
                                </div>

                                <div>
                                    <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Country
                                    </label>
                                    <select
                                        id="country_id"
                                        v-model="form.country_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-500': form.errors.country_id }"
                                    >
                                        <option value="">Select Country</option>
                                        <option v-for="country in countries" :key="country.id" :value="country.id">
                                            {{ country.name }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.country_id" class="mt-1 text-sm text-red-600">{{ form.errors.country_id }}</p>
                                </div>
                            </div>

                            <!-- Names -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Name (English) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-500': form.errors.name }"
                                        required
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>

                                <div>
                                    <label for="name_bn" class="block text-sm font-medium text-gray-700 mb-1">
                                        Name (Bengali) <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="name_bn"
                                        v-model="form.name_bn"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-500': form.errors.name_bn }"
                                        placeholder="বাংলায় নাম"
                                        required
                                    />
                                    <p v-if="form.errors.name_bn" class="mt-1 text-sm text-red-600">{{ form.errors.name_bn }}</p>
                                </div>
                            </div>

                            <!-- Slug (Read-only) -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                                    Slug
                                </label>
                                <input
                                    id="slug"
                                    :value="directory.slug"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm cursor-not-allowed"
                                    readonly
                                />
                                <p class="mt-1 text-xs text-gray-500">Auto-generated from name</p>
                            </div>

                            <!-- Descriptions -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description (English)
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.description }"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <div>
                                <label for="description_bn" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description (Bengali)
                                </label>
                                <textarea
                                    id="description_bn"
                                    v-model="form.description_bn"
                                    rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.description_bn }"
                                    placeholder="বাংলায় বিবরণ"
                                ></textarea>
                                <p v-if="form.errors.description_bn" class="mt-1 text-sm text-red-600">{{ form.errors.description_bn }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email
                                    </label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-500': form.errors.email }"
                                    />
                                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone
                                    </label>
                                    <input
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-500': form.errors.phone }"
                                    />
                                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                                </div>
                            </div>

                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
                                    Website URL
                                </label>
                                <input
                                    id="website"
                                    v-model="form.website"
                                    type="url"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.website }"
                                    placeholder="https://"
                                />
                                <p v-if="form.errors.website" class="mt-1 text-sm text-red-600">{{ form.errors.website }}</p>
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Address (English)
                                </label>
                                <textarea
                                    id="address"
                                    v-model="form.address"
                                    rows="2"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.address }"
                                ></textarea>
                                <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                            </div>

                            <div>
                                <label for="address_bn" class="block text-sm font-medium text-gray-700 mb-1">
                                    Address (Bengali)
                                </label>
                                <textarea
                                    id="address_bn"
                                    v-model="form.address_bn"
                                    rows="2"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.address_bn }"
                                    placeholder="বাংলায় ঠিকানা"
                                ></textarea>
                                <p v-if="form.errors.address_bn" class="mt-1 text-sm text-red-600">{{ form.errors.address_bn }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Location (GPS Coordinates) -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Location</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">
                                    Latitude
                                </label>
                                <input
                                    id="latitude"
                                    v-model="form.latitude"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.latitude }"
                                    placeholder="23.8103"
                                />
                                <p v-if="form.errors.latitude" class="mt-1 text-sm text-red-600">{{ form.errors.latitude }}</p>
                            </div>

                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">
                                    Longitude
                                </label>
                                <input
                                    id="longitude"
                                    v-model="form.longitude"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.longitude }"
                                    placeholder="90.4125"
                                />
                                <p v-if="form.errors.longitude" class="mt-1 text-sm text-red-600">{{ form.errors.longitude }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Images</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Logo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Logo
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <div v-if="logoPreview || directory.logo_url" class="mb-4">
                                            <img :src="logoPreview || directory.logo_url" alt="Logo" class="mx-auto h-32 w-32 object-cover rounded-lg">
                                            <button
                                                @click="removeLogo"
                                                type="button"
                                                class="mt-2 text-sm text-red-600 hover:text-red-800"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <svg v-else class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                                <span>Upload logo</span>
                                                <input
                                                    id="logo"
                                                    type="file"
                                                    class="sr-only"
                                                    accept="image/*"
                                                    @change="handleLogoUpload"
                                                />
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                </div>
                                <p v-if="form.errors.logo" class="mt-1 text-sm text-red-600">{{ form.errors.logo }}</p>
                            </div>

                            <!-- Featured Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Featured Image
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <div v-if="imagePreview || directory.image_url" class="mb-4">
                                            <img :src="imagePreview || directory.image_url" alt="Image" class="mx-auto h-32 w-auto object-cover rounded-lg">
                                            <button
                                                @click="removeImage"
                                                type="button"
                                                class="mt-2 text-sm text-red-600 hover:text-red-800"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <svg v-else class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                                <span>Upload image</span>
                                                <input
                                                    id="image"
                                                    type="file"
                                                    class="sr-only"
                                                    accept="image/*"
                                                    @change="handleImageUpload"
                                                />
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                    </div>
                                </div>
                                <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">SEO</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                                    Meta Title
                                </label>
                                <input
                                    id="meta_title"
                                    v-model="form.meta_title"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.meta_title }"
                                    maxlength="60"
                                />
                                <p class="mt-1 text-xs text-gray-500">{{ form.meta_title?.length || 0 }}/60 characters</p>
                                <p v-if="form.errors.meta_title" class="mt-1 text-sm text-red-600">{{ form.errors.meta_title }}</p>
                            </div>

                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Meta Description
                                </label>
                                <textarea
                                    id="meta_description"
                                    v-model="form.meta_description"
                                    rows="2"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.meta_description }"
                                    maxlength="160"
                                ></textarea>
                                <p class="mt-1 text-xs text-gray-500">{{ form.meta_description?.length || 0 }}/160 characters</p>
                                <p v-if="form.errors.meta_description" class="mt-1 text-sm text-red-600">{{ form.errors.meta_description }}</p>
                            </div>

                            <div>
                                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">
                                    Meta Keywords
                                </label>
                                <input
                                    id="meta_keywords"
                                    v-model="form.meta_keywords"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    :class="{ 'border-red-500': form.errors.meta_keywords }"
                                    placeholder="keyword1, keyword2, keyword3"
                                />
                                <p class="mt-1 text-xs text-gray-500">Separate keywords with commas</p>
                                <p v-if="form.errors.meta_keywords" class="mt-1 text-sm text-red-600">{{ form.errors.meta_keywords }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Settings -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status & Settings</h3>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_published"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Published (visible to public)</span>
                            </label>

                            <label class="flex items-center">
                                <input
                                    v-model="form.is_verified"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Verified</span>
                            </label>

                            <label class="flex items-center">
                                <input
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                                <span class="ml-2 text-sm text-gray-700">Featured</span>
                            </label>
                        </div>

                        <!-- Statistics -->
                        <div class="mt-6 pt-6 border-t">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Statistics</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <div class="text-2xl font-bold text-blue-600">{{ directory.views_count || 0 }}</div>
                                    <div class="text-xs text-gray-600">Views</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600">Created</div>
                                    <div class="text-xs text-gray-500">{{ formatDate(directory.created_at) }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600">Updated</div>
                                    <div class="text-xs text-gray-500">{{ formatDate(directory.updated_at) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-3">
                        <Link
                            :href="route('admin.directories.index')"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Updating...</span>
                            <span v-else>Update Directory</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    directory: Object,
    categories: Array,
    countries: Array
});

const form = useForm({
    directory_category_id: props.directory.directory_category_id,
    country_id: props.directory.country_id,
    name: props.directory.name,
    name_bn: props.directory.name_bn,
    description: props.directory.description,
    description_bn: props.directory.description_bn,
    email: props.directory.email,
    phone: props.directory.phone,
    website: props.directory.website,
    address: props.directory.address,
    address_bn: props.directory.address_bn,
    latitude: props.directory.latitude,
    longitude: props.directory.longitude,
    logo: null,
    image: null,
    meta_title: props.directory.meta_title,
    meta_description: props.directory.meta_description,
    meta_keywords: props.directory.meta_keywords,
    is_published: props.directory.is_published,
    is_verified: props.directory.is_verified,
    is_featured: props.directory.is_featured
});

const logoPreview = ref(null);
const imagePreview = ref(null);

const handleLogoUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const removeLogo = () => {
    form.logo = null;
    logoPreview.value = null;
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const submit = () => {
    form.post(route('admin.directories.update', props.directory.id), {
        _method: 'put',
        preserveScroll: true,
        onSuccess: () => {
            // Will redirect to index on success
        }
    });
};
</script>
