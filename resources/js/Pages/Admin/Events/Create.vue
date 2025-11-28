<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    title: '',
    slug: '',
    description: '',
    event_type: 'seminar',
    event_date: '',
    event_time: '',
    end_date: '',
    end_time: '',
    location: '',
    is_online: false,
    meeting_link: '',
    image: null,
    max_participants: '',
    registration_deadline: '',
    is_featured: false,
    is_published: true,
});

// Auto-generate slug from title
watch(() => form.title, (newTitle) => {
    if (newTitle && !form.slug) {
        form.slug = newTitle
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/--+/g, '-')
            .trim();
    }
});

const imagePreview = ref(null);

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.image = null;
    imagePreview.value = null;
};

const submit = () => {
    form.post(route('admin.events.store'), {
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
        }
    });
};
</script>

<template>
    <Head title="Create Event" />

    <AdminLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <Link
                    :href="route('admin.events.index')"
                    class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6"
                >
                    <ArrowLeftIcon class="h-5 w-5 mr-2" />
                    Back to Events
                </Link>

                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Event</h1>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Basic Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="e.g., Student Visa Application Workshop"
                                    />
                                    <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
                                    <input
                                        v-model="form.slug"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono text-sm"
                                        placeholder="auto-generated from title"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate from title</p>
                                    <div v-if="form.errors.slug" class="text-red-600 text-sm mt-1">{{ form.errors.slug }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                                    <select
                                        v-model="form.event_type"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="seminar">Seminar</option>
                                        <option value="workshop">Workshop</option>
                                        <option value="webinar">Webinar</option>
                                        <option value="fair">Fair</option>
                                        <option value="consultation">Consultation</option>
                                    </select>
                                    <div v-if="form.errors.event_type" class="text-red-600 text-sm mt-1">{{ form.errors.event_type }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                    <textarea
                                        v-model="form.description"
                                        required
                                        rows="5"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="Provide a detailed description of the event..."
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Date & Time</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Date *</label>
                                    <input
                                        v-model="form.event_date"
                                        type="date"
                                        required
                                        :min="new Date().toISOString().split('T')[0]"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.event_date" class="text-red-600 text-sm mt-1">{{ form.errors.event_date }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Event Time *</label>
                                    <input
                                        v-model="form.event_time"
                                        type="time"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.event_time" class="text-red-600 text-sm mt-1">{{ form.errors.event_time }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                    <input
                                        v-model="form.end_date"
                                        type="date"
                                        :min="form.event_date || new Date().toISOString().split('T')[0]"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">For multi-day events</p>
                                    <div v-if="form.errors.end_date" class="text-red-600 text-sm mt-1">{{ form.errors.end_date }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                    <input
                                        v-model="form.end_time"
                                        type="time"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                    <div v-if="form.errors.end_time" class="text-red-600 text-sm mt-1">{{ form.errors.end_time }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Registration Deadline</label>
                                    <input
                                        v-model="form.registration_deadline"
                                        type="date"
                                        :max="form.event_date"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">Last date to register</p>
                                    <div v-if="form.errors.registration_deadline" class="text-red-600 text-sm mt-1">{{ form.errors.registration_deadline }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Location</h2>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input
                                        v-model="form.is_online"
                                        type="checkbox"
                                        id="is_online"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <label for="is_online" class="ml-2 text-sm font-medium text-gray-700">
                                        This is an online event
                                    </label>
                                </div>

                                <div v-if="form.is_online">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Meeting Link</label>
                                    <input
                                        v-model="form.meeting_link"
                                        type="url"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="https://zoom.us/j/..."
                                    />
                                    <div v-if="form.errors.meeting_link" class="text-red-600 text-sm mt-1">{{ form.errors.meeting_link }}</div>
                                </div>

                                <div v-else>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Physical Location</label>
                                    <input
                                        v-model="form.location"
                                        type="text"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="e.g., Hotel Radisson, Dhaka"
                                    />
                                    <div v-if="form.errors.location" class="text-red-600 text-sm mt-1">{{ form.errors.location }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Event Image -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Image</h2>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageChange"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                />
                                <p class="text-xs text-gray-500 mt-1">Max size: 2MB (JPG, PNG, WebP)</p>
                                <div v-if="form.errors.image" class="text-red-600 text-sm mt-1">{{ form.errors.image }}</div>

                                <div v-if="imagePreview" class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                                    <div class="relative inline-block">
                                        <img :src="imagePreview" alt="Preview" class="h-48 rounded-lg shadow-sm" />
                                        <button
                                            type="button"
                                            @click="removeImage"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Settings -->
                        <div class="border-b border-gray-200 pb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Settings</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Participants</label>
                                    <input
                                        v-model="form.max_participants"
                                        type="number"
                                        min="1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                        placeholder="Leave empty for unlimited"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">Maximum number of people who can register</p>
                                    <div v-if="form.errors.max_participants" class="text-red-600 text-sm mt-1">{{ form.errors.max_participants }}</div>
                                </div>

                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center">
                                        <input
                                            v-model="form.is_featured"
                                            type="checkbox"
                                            id="is_featured"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                        <label for="is_featured" class="ml-2 text-sm font-medium text-gray-700">
                                            Featured Event
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input
                                            v-model="form.is_published"
                                            type="checkbox"
                                            id="is_published"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                        />
                                        <label for="is_published" class="ml-2 text-sm font-medium text-gray-700">
                                            Publish Immediately
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-6">
                            <Link
                                :href="route('admin.events.index')"
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Event</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
