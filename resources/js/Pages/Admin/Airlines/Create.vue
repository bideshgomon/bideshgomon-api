<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    name: '',
    iata_code: '',
});

const submit = () => {
    // PATCH: Submit to the API route
    form.post(route('api.admin.airlines.store'), {
        onSuccess: () => form.reset(), // Reset form on success
    });
};
</script>

<template>
    <Head title="Add New Airline" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Add New Airline
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Enter the details for the new airline.
                    </p>
                </header>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Airline Name" />
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
                        <InputLabel for="iata_code" value="Airline IATA Code (2 Letters)" />
                        <TextInput
                            id="iata_code"
                            type="text"
                            class="mt-1 block w-full uppercase"
                            v-model="form.iata_code"
                            required
                            maxlength="2"
                            minlength="2"
                        />
                        <InputError class="mt-2" :message="form.errors.iata_code" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Create Airline' }}
                        </PrimaryButton>

                        <Link
                            :href="route('admin.airlines.index')"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>