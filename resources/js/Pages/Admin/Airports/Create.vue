<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    countries: Array, // Expecting Array of Countries -> States -> Cities
});

const form = useForm({
    name: '',
    iata_code: '',
    city_id: '',
});

const submit = () => {
    // Post to the API endpoint for creation
    form.post(route('api.admin.airports.store'), {
        onSuccess: () => form.reset(), // Clear form on success
        // onError: handled by form.errors
    });
};
</script>

<template>
    <Head title="Add New Airport" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Add New Airport
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Enter the details for the new airport.
                    </p>
                </header>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Airport Name" />
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
                        <InputLabel for="iata_code" value="Airport IATA Code (3 Letters)" />
                        <TextInput
                            id="iata_code"
                            type="text"
                            class="mt-1 block w-full uppercase"
                            v-model="form.iata_code"
                            required
                            maxlength="3"
                            minlength="3"
                        />
                        <InputError class="mt-2" :message="form.errors.iata_code" />
                    </div>

                    <div>
                        <InputLabel for="city_id" value="City" />
                        <select
                            id="city_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            v-model="form.city_id"
                            required
                        >
                            <option value="" disabled>Select a city</option>
                            <optgroup v-for="country in countries" :key="country.id" :label="country.name">
                                <template v-for="state in country.states" :key="state.id">
                                     <option v-if="!state.cities || state.cities.length === 0" disabled>-- No cities in {{ state.name }} --</option>
                                     <option v-for="city in state.cities" :key="city.id" :value="city.id">
                                         {{ city.name }} ({{ state.name }})
                                     </option>
                                </template>
                            </optgroup>
                        </select>
                        <InputError class="mt-2" :message="form.errors.city_id" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Create Airport' }}
                        </PrimaryButton>

                        <Link
                            :href="route('admin.airports.index')"
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