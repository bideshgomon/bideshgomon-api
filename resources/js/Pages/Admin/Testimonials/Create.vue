<template>
    <AdminLayout title="Create Testimonial">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <Link :href="route('admin.testimonials.index')" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Testimonials
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New Testimonial</h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Author Name -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Author Name (English) *
                            </label>
                            <input
                                v-model="form.author_name"
                                type="text"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.author_name }"
                            />
                            <div v-if="form.errors.author_name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.author_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Author Name (Bengali)
                            </label>
                            <input
                                v-model="form.author_name_bn"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <!-- Position -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Position (English)
                            </label>
                            <input
                                v-model="form.position"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Position (Bengali)
                            </label>
                            <input
                                v-model="form.position_bn"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <!-- Company -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Company (English)
                            </label>
                            <input
                                v-model="form.company"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Company (Bengali)
                            </label>
                            <input
                                v-model="form.company_bn"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Location
                        </label>
                        <input
                            v-model="form.location"
                            type="text"
                            placeholder="e.g., Dhaka, Bangladesh"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        />
                    </div>

                    <!-- Content -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Testimonial Content (English) *
                            </label>
                            <textarea
                                v-model="form.content"
                                required
                                rows="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.content }"
                            />
                            <div v-if="form.errors.content" class="mt-1 text-sm text-red-600">
                                {{ form.errors.content }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Testimonial Content (Bengali)
                            </label>
                            <textarea
                                v-model="form.content_bn"
                                rows="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            />
                        </div>
                    </div>

                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Rating *
                        </label>
                        <div class="flex items-center gap-2">
                            <button
                                v-for="star in 5"
                                :key="star"
                                type="button"
                                @click="form.rating = star"
                                class="focus:outline-none"
                            >
                                <svg
                                    class="h-8 w-8 transition-colors"
                                    :class="star <= form.rating ? 'text-yellow-400' : 'text-gray-300'"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                            <span class="ml-2 text-sm text-gray-600">{{ form.rating }} star{{ form.rating !== 1 ? 's' : '' }}</span>
                        </div>
                        <div v-if="form.errors.rating" class="mt-1 text-sm text-red-600">
                            {{ form.errors.rating }}
                        </div>
                    </div>

                    <!-- Photo Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Author Photo
                        </label>
                        <input
                            @change="handleFileChange"
                            type="file"
                            accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        />
                        <p class="mt-1 text-sm text-gray-500">Maximum file size: 2MB</p>
                        <div v-if="imagePreview" class="mt-3">
                            <img :src="imagePreview" alt="Preview" class="w-24 h-24 rounded-full object-cover"/>
                        </div>
                        <div v-if="form.errors.photo" class="mt-1 text-sm text-red-600">
                            {{ form.errors.photo }}
                        </div>
                    </div>

                    <!-- Checkboxes -->
                    <div class="flex flex-col gap-3">
                        <label class="flex items-center">
                            <input
                                v-model="form.is_featured"
                                type="checkbox"
                                class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                            />
                            <span class="ml-2 text-sm font-medium text-gray-700">Featured on Homepage</span>
                        </label>

                        <label class="flex items-center">
                            <input
                                v-model="form.is_approved"
                                type="checkbox"
                                class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                            />
                            <span class="ml-2 text-sm font-medium text-gray-700">Approved</span>
                        </label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Creating...' : 'Create Testimonial' }}
                        </button>
                        <Link
                            :href="route('admin.testimonials.index')"
                            class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const imagePreview = ref(null);

const form = useForm({
    author_name: '',
    author_name_bn: '',
    position: '',
    position_bn: '',
    company: '',
    company_bn: '',
    content: '',
    content_bn: '',
    rating: 5,
    photo: null,
    location: '',
    is_featured: false,
    is_approved: false,
});

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('admin.testimonials.store'));
};
</script>
