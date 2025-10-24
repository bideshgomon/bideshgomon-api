<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import Textarea from '@/Components/Textarea.vue';
import { ref } from 'vue';

const props = defineProps({
    service: Object,
});

const form = useForm({
    name: props.service.name,
    description: props.service.description,
    price: props.service.price,
    duration_minutes: props.service.duration_minutes,
    is_active: props.service.is_active,
});

const submit = () => {
    form.put(route('admin.consultation-services.update', props.service.id));
};

const confirmDelete = () => {
    if (window.confirm('Are you sure you want to delete this service?')) {
        useForm({}).delete(route('admin.consultation-services.destroy', props.service.id), {
            preserveScroll: true,
        });
    }
};

</script>

<template>
    <Head title="Edit Consultation Service" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Consultation Service
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <InputLabel for="name" value="Service Name" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div>
                                    <InputLabel for="description" value="Description" />
                                    <Textarea
                                        id="description"
                                        class="mt-1 block w-full"
                                        v-model="form.description"
                                        rows="4"
                                    />
                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="price" value="Price ($)" />
                                        <TextInput
                                            id="price"
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="form.price"
                                            required
                                            min="0"
                                            step="0.01"
                                        />
                                        <InputError class="mt-2" :message="form.errors.price" />
                                    </div>
                                    <div>
                                        <InputLabel for="duration_minutes" value="Duration (minutes)" />
                                        <TextInput
                                            id="duration_minutes"
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="form.duration_minutes"
                                            required
                                            min="1"
                                        />
                                        <InputError class="mt-2" :message="form.errors.duration_minutes" />
                                    </div>
                                </div>
                                
                                <div class="block">
                                    <label class="flex items-center">
                                        <Checkbox name="is_active" v-model:checked="form.is_active" />
                                        <span class="ms-2 text-sm text-gray-600">Active (Visible to users)</span>
                                    </label>
                                    <InputError class="mt-2" :message="form.errors.is_active" />
                                </div>

                            </div>

                            <div class="flex items-center justify-between mt-6">
                                <div>
                                    <DangerButton type="button" @click="confirmDelete">
                                        Delete Service
                                    </DangerButton>
                                </div>
                                <div class="flex items-center">
                                    <Link :href="route('admin.consultation-services.index')" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                        Cancel
                                    </Link>
                                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Update Service
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>