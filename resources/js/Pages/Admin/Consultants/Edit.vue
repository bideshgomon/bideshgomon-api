<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Textarea from '@/Components/Textarea.vue';
import { ref } from 'vue';

const props = defineProps({
    consultant: Object,
    profile: Object, // This is the consultant_profile (or default object)
});

const form = useForm({
    title: props.profile.title,
    bio: props.profile.bio,
    // We handle specializations as a simple string for this form
    // and split/join it for the array.
    specializations: props.profile.specializations.join(', '), 
});

const submit = () => {
    // Transform the comma-separated string back into an array
    const formData = {
        ...form,
        specializations: form.specializations.split(',')
                             .map(s => s.trim())
                             .filter(s => s.length > 0),
    };

    useForm(formData).put(route('admin.consultants.update', props.consultant.id));
};

</script>

<template>
    <Head title="Edit Consultant Profile" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Profile for: {{ consultant.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <InputLabel for="name" value="Consultant Name" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full bg-gray-100"
                                        :value="consultant.name"
                                        disabled
                                    />
                                </div>

                                <div>
                                    <InputLabel for="email" value="Consultant Email" />
                                    <TextInput
                                        id="email"
                                        type="email"
                                        class="mt-1 block w-full bg-gray-100"
                                        :value="consultant.email"
                                        disabled
                                    />
                                </div>

                                <hr />

                                <div>
                                    <InputLabel for="title" value="Professional Title" />
                                    <TextInput
                                        id="title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.title"
                                        required
                                        autofocus
                                        placeholder="e.g., Senior Immigration Consultant"
                                    />
                                    <InputError class="mt-2" :message="form.errors.title" />
                                </div>

                                <div>
                                    <InputLabel for="bio" value="Professional Bio" />
                                    <Textarea
                                        id="bio"
                                        class="mt-1 block w-full"
                                        v-model="form.bio"
                                        rows="5"
                                        placeholder="A short biography..."
                                    />
                                    <InputError class="mt-2" :message="form.errors.bio" />
                                </div>

                                <div>
                                    <InputLabel for="specializations" value="Specializations" />
                                    <TextInput
                                        id="specializations"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.specializations"
                                        placeholder="e.g., Student Visa, Work Visa, PR"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">
                                        Enter specializations separated by commas.
                                    </p>
                                    <InputError class="mt-2" :message="form.errors.specializations" />
                                </div>

                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link :href="route('admin.consultants.index')" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                    Cancel
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update Profile
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>