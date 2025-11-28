<template>
    <Head title="Add Team Member" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Add Team Member</h2>
                            <Link :href="route('agency.team.index')" class="text-indigo-600 hover:text-indigo-900">
                                Back to Team
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Photo Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Photo</label>
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        <img 
                                            v-if="photoPreview" 
                                            :src="photoPreview" 
                                            alt="Photo preview"
                                            class="h-24 w-24 rounded-full object-cover"
                                        />
                                        <div v-else class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center">
                                            <UserIcon class="h-12 w-12 text-gray-400" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <input 
                                            type="file" 
                                            @change="handlePhotoChange" 
                                            accept="image/jpeg,image/png,image/jpg"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">JPG, PNG. Max 2MB.</p>
                                    </div>
                                </div>
                                <p v-if="form.errors.photo" class="mt-2 text-sm text-red-600">{{ form.errors.photo }}</p>
                            </div>

                            <!-- Basic Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name *</label>
                                    <input 
                                        v-model="form.name" 
                                        type="text" 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Position *</label>
                                    <input 
                                        v-model="form.position" 
                                        type="text" 
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <p v-if="form.errors.position" class="mt-1 text-sm text-red-600">{{ form.errors.position }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input 
                                        v-model="form.email" 
                                        type="email" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input 
                                        v-model="form.phone" 
                                        type="text" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Years of Experience</label>
                                    <input 
                                        v-model.number="form.years_experience" 
                                        type="number" 
                                        min="0"
                                        max="50"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <p v-if="form.errors.years_experience" class="mt-1 text-sm text-red-600">{{ form.errors.years_experience }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Visibility</label>
                                    <select 
                                        v-model="form.is_visible" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option :value="true">Visible on Profile</option>
                                        <option :value="false">Hidden</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Bio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Bio</label>
                                <textarea 
                                    v-model="form.bio" 
                                    rows="4" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                                <p v-if="form.errors.bio" class="mt-1 text-sm text-red-600">{{ form.errors.bio }}</p>
                            </div>

                            <!-- Languages -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Languages</label>
                                <div class="space-y-2">
                                    <div v-for="(language, index) in form.languages" :key="index" class="flex items-center space-x-2">
                                        <input 
                                            v-model="form.languages[index]" 
                                            type="text" 
                                            placeholder="e.g., English, Bengali"
                                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        <button 
                                            v-if="form.languages.length > 1"
                                            type="button" 
                                            @click="removeLanguage(index)" 
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            <XMarkIcon class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                                <button 
                                    type="button" 
                                    @click="addLanguage" 
                                    class="mt-2 text-sm text-indigo-600 hover:text-indigo-900"
                                >
                                    + Add Language
                                </button>
                            </div>

                            <!-- Qualifications -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Qualifications</label>
                                <div class="space-y-2">
                                    <div v-for="(qualification, index) in form.qualifications" :key="index" class="flex items-center space-x-2">
                                        <input 
                                            v-model="form.qualifications[index]" 
                                            type="text" 
                                            placeholder="e.g., MBA, Certified Immigration Consultant"
                                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        <button 
                                            v-if="form.qualifications.length > 1"
                                            type="button" 
                                            @click="removeQualification(index)" 
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            <XMarkIcon class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                                <button 
                                    type="button" 
                                    @click="addQualification" 
                                    class="mt-2 text-sm text-indigo-600 hover:text-indigo-900"
                                >
                                    + Add Qualification
                                </button>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex justify-end space-x-4">
                                <Link 
                                    :href="route('agency.team.index')" 
                                    class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Cancel
                                </Link>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing"
                                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Adding...' : 'Add Team Member' }}
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
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { UserIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: '',
    position: '',
    email: '',
    phone: '',
    photo: null,
    bio: '',
    years_experience: null,
    languages: [''],
    qualifications: [''],
    is_visible: true,
});

const photoPreview = ref(null);

const handlePhotoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const addLanguage = () => {
    form.languages.push('');
};

const removeLanguage = (index) => {
    form.languages.splice(index, 1);
};

const addQualification = () => {
    form.qualifications.push('');
};

const removeQualification = (index) => {
    form.qualifications.splice(index, 1);
};

const submit = () => {
    form.post(route('agency.team.store'), {
        preserveScroll: true,
    });
};
</script>
