<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
    is_active: true,
});

const submit = () => {
    form.post(route('api.admin.job-categories.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Add Job Category" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add New Job Category</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Enter details for the new job category.</p>
                </header>

                <form @submit.prevent="submit" class="space-y-6 max-w-xl">
                    <div>
                        <InputLabel for="name" value="Category Name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="description" value="Description (Optional)" />
                        <TextareaInput id="description" class="mt-1 block w-full" v-model="form.description" rows="3" />
                        <InputError class="mt-2" :message="form.errors.description" />
                    </div>
                     <div class="block">
                        <label class="flex items-center">
                            <Checkbox name="is_active" v-model:checked="form.is_active" />
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Active</span>
                        </label>
                        <InputError class="mt-2" :message="form.errors.is_active" />
                    </div>
                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">{{ form.processing ? 'Saving...' : 'Save Category' }}</PrimaryButton>
                        <Link :href="route('admin.job-categories.index')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-25 transition ease-in-out duration-150">Cancel</Link>
                    </div>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>