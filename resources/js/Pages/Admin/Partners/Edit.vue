<template>
    <AdminLayout title="Edit Partner">
        <div class="max-w-3xl mx-auto">
            <div class="mb-6">
                <Link :href="route('admin.partners.index')" class="text-blue-600 hover:text-blue-800">
                    ← Back to Partners
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Partner</h1>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Partner Name *</label>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            required
                            class="form-input w-full"
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                        
                        <div v-if="partner.logo_url" class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Current Logo:</p>
                            <img :src="partner.logo_url" :alt="partner.name" class="h-24 w-auto object-contain border border-gray-200 rounded p-2"/>
                        </div>
                        
                        <input 
                            @change="handleFileChange" 
                            type="file" 
                            accept="image/*"
                            class="form-input w-full"
                            :class="{ 'border-red-500': form.errors.logo }"
                        />
                        <p class="mt-1 text-sm text-gray-500">Leave empty to keep current logo. Accepted formats: JPG, PNG, WebP, SVG (Max: 2MB)</p>
                        <p v-if="form.errors.logo" class="mt-1 text-sm text-red-600">{{ form.errors.logo }}</p>
                        
                        <div v-if="previewUrl" class="mt-4">
                            <p class="text-sm text-gray-600 mb-2">New Logo Preview:</p>
                            <img :src="previewUrl" alt="Preview" class="h-24 w-auto object-contain border border-gray-200 rounded p-2"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                        <input 
                            v-model="form.website_url" 
                            type="url" 
                            placeholder="https://example.com"
                            class="form-input w-full"
                            :class="{ 'border-red-500': form.errors.website_url }"
                        />
                        <p v-if="form.errors.website_url" class="mt-1 text-sm text-red-600">{{ form.errors.website_url }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                        <input 
                            v-model.number="form.sort_order" 
                            type="number" 
                            min="0"
                            class="form-input w-full"
                            :class="{ 'border-red-500': form.errors.sort_order }"
                        />
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                        <p v-if="form.errors.sort_order" class="mt-1 text-sm text-red-600">{{ form.errors.sort_order }}</p>
                    </div>

                    <div class="flex items-center">
                        <input 
                            v-model="form.is_active" 
                            type="checkbox" 
                            id="is_active"
                            class="form-checkbox h-4 w-4 text-blue-600"
                        />
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4">
                        <Link :href="route('admin.partners.index')" class="btn btn-secondary">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="btn btn-primary">
                            <span v-if="form.processing">Updating...</span>
                            <span v-else>Update Partner</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    partner: Object
});

const form = useForm({
    name: props.partner.name,
    logo: null,
    website_url: props.partner.website_url || '',
    sort_order: props.partner.sort_order,
    is_active: props.partner.is_active,
    _method: 'PUT'
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
    form.post(route('admin.partners.update', props.partner.id));
};
</script>
