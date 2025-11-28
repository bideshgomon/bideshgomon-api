<template>
    <AdminLayout title="Create Partner">
        <div class="py-6">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('admin.partners.index')" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Partners
                    </Link>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Add New Partner</h1>
                        <p class="mt-1 text-sm text-gray-600">Create a new partner profile with logo and details</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Partner Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                required
                                placeholder="Enter partner name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Name (Bengali) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Partner Name (Bengali)
                            </label>
                            <input 
                                v-model="form.name_bn" 
                                type="text" 
                                placeholder="পার্টনার নাম"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.name_bn }"
                            />
                            <p v-if="form.errors.name_bn" class="mt-1 text-sm text-red-600">{{ form.errors.name_bn }}</p>
                        </div>

                        <!-- Logo Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Logo <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg v-if="!previewUrl" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div v-if="previewUrl" class="mb-4">
                                        <img :src="previewUrl" alt="Preview" class="mx-auto h-32 w-auto object-contain border border-gray-200 rounded p-2 bg-gray-50"/>
                                    </div>
                                    <div class="flex text-sm text-gray-600">
                                        <label class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                            <span>Upload a file</span>
                                            <input 
                                                @change="handleFileChange" 
                                                type="file" 
                                                accept="image/*"
                                                required
                                                class="sr-only"
                                            />
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, WebP, SVG up to 2MB</p>
                                </div>
                            </div>
                            <p v-if="form.errors.logo" class="mt-1 text-sm text-red-600">{{ form.errors.logo }}</p>
                        </div>

                        <!-- Website URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                </div>
                                <input 
                                    v-model="form.website_url" 
                                    type="url" 
                                    placeholder="https://example.com"
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                    :class="{ 'border-red-500': form.errors.website_url }"
                                />
                            </div>
                            <p v-if="form.errors.website_url" class="mt-1 text-sm text-red-600">{{ form.errors.website_url }}</p>
                        </div>

                        <!-- Partner Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Partner Type</label>
                            <select 
                                v-model="form.partner_type" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.partner_type }"
                            >
                                <option value="client">Client</option>
                                <option value="partner">Partner</option>
                                <option value="sponsor">Sponsor</option>
                                <option value="affiliate">Affiliate</option>
                            </select>
                            <p v-if="form.errors.partner_type" class="mt-1 text-sm text-red-600">{{ form.errors.partner_type }}</p>
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                            <input 
                                v-model.number="form.sort_order" 
                                type="number" 
                                min="0"
                                placeholder="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.sort_order }"
                            />
                            <p class="mt-1 text-xs text-gray-500">Lower numbers appear first. Leave as 0 for default ordering.</p>
                            <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">{{ form.errors.sort_order }}</p>
                        </div>

                        <!-- Status Toggle -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <label for="is_active" class="text-sm font-medium text-gray-700">Active Status</label>
                                <p class="text-xs text-gray-500">Enable this partner to appear on the website</p>
                            </div>
                            <button
                                type="button"
                                @click="form.is_active = !form.is_active"
                                :class="form.is_active ? 'bg-primary-600' : 'bg-gray-200'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                            >
                                <span
                                    :class="form.is_active ? 'translate-x-5' : 'translate-x-0'"
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                />
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <Link :href="route('admin.partners.index')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel
                            </Link>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="form.processing" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating...
                                </span>
                                <span v-else>Create Partner</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const form = useForm({
    name: '',
    name_bn: '',
    logo: null,
    website_url: '',
    partner_type: 'client',
    sort_order: 0,
    is_active: true
});

const previewUrl = ref(null);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('admin.partners.store'));
};
</script>
