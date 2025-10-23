<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Define props passed from the controller
const props = defineProps({
    countries: Array,
});

// Initialize the form state
const form = useForm({
    name: '',
    country_id: '',
    city: '',
    website_url: '',
    logo: null,
    intake_months: [], // Start with empty array
    ranking: '',
});

// Handle file input changes
const onLogoChange = (event) => {
    form.logo = event.target.files[0];
};

// Handle intake month checkboxes
const handleIntakeChange = (event) => {
    const month = event.target.value;
    if (event.target.checked) {
        if (!form.intake_months.includes(month)) {
            form.intake_months.push(month);
        }
    } else {
        form.intake_months = form.intake_months.filter(m => m !== month);
    }
};

// Submit the form
const submit = () => {
    form.post(route('api.admin.universities.store'), {
        // Automatically redirects to index on success
        onSuccess: () => form.reset(),
    });
};

const intakeOptions = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];
</script>

<template>
    <Head title="Add New University" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Add New University
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Enter the details for the new university.
                    </p>
                </header>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="University Name" />
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
                        <InputLabel for="country_id" value="Country" />
                        <select
                            id="country_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            v-model="form.country_id"
                            required
                        >
                            <option value="" disabled>Select a country</option>
                            <option v-for="country in countries" :key="country.id" :value="country.id">
                                {{ country.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.country_id" />
                    </div>

                    <div>
                        <InputLabel for="city" value="City" />
                        <TextInput
                            id="city"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.city"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.city" />
                    </div>

                    <div>
                        <InputLabel for="website_url" value="Website URL (Optional)" />
                        <TextInput
                            id="website_url"
                            type="url"
                            class="mt-1 block w-full"
                            v-model="form.website_url"
                        />
                        <InputError class="mt-2" :message="form.errors.website_url" />
                    </div>

                    <div>
                        <InputLabel for="logo" value="Logo (Optional, Max 2MB)" />
                        <input
                            id="logo"
                            type="file"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            @change="onLogoChange"
                            accept="image/png, image/jpeg, image/jpg"
                        />
                        <InputError class="mt-2" :message="form.errors.logo" />
                    </div>

                    <div>
                        <InputLabel value="Intake Months (Optional)" />
                        <div class="mt-2 grid grid-cols-3 gap-4">
                            <div v-for="month in intakeOptions" :key="month" class="flex items-center">
                                <input
                                    :id="'intake_' + month"
                                    type="checkbox"
                                    :value="month"
                                    @change="handleIntakeChange"
                                    class="rounded border-gray-300 text-brand-primary focus:ring-brand-primary"
                                />
                                <label :for="'intake_' + month" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">{{ month }}</label>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.intake_months" />
                    </div>

                    <div>
                        <InputLabel for="ranking" value="Ranking (Optional)" />
                        <TextInput
                            id="ranking"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.ranking"
                        />
                        <InputError class="mt-2" :message="form.errors.ranking" />
                    </div>

                    <div class="flex items-center gap-4">
                        <PrimaryButton :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save University' }}
                        </PrimaryButton>

                        <Link
                            :href="route('admin.universities.index')"
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