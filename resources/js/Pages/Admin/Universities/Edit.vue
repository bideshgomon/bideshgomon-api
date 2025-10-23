<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import DangerButton from '@/Components/DangerButton.vue'; // For Delete
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Define props passed from the controller
const props = defineProps({
    university: Object,
    countries: Array,
});

// Initialize the form state with existing university data
const form = useForm({
    _method: 'PUT', // Important for Laravel update route
    name: props.university.name,
    country_id: props.university.country_id,
    city: props.university.city,
    website_url: props.university.website_url || '',
    logo: null, // We handle logo updates separately
    intake_months: props.university.intake_months || [],
    ranking: props.university.ranking || '',
});

// Ref to display existing logo
const existingLogoUrl = ref(props.university.logo_path ? `/storage/${props.university.logo_path}` : null);
const logoPreviewUrl = ref(null);

// Handle file input changes and preview
const onLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreviewUrl.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        form.logo = null;
        logoPreviewUrl.value = null;
    }
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

// Submit the form (using router.post for file uploads)
const submit = () => {
    // Inertia needs 'post' for file uploads, even with PUT method override
    router.post(route('api.admin.universities.update', props.university.id), form, {
        forceFormData: true, // Crucial for file uploads
        onSuccess: () => {
            // Optionally clear the file input if needed
            // Redirect happens automatically based on API response
        },
    });
};

// Delete university
const deleteUniversity = () => {
    if (confirm('Are you sure you want to delete this university? This action cannot be undone.')) {
        router.delete(route('api.admin.universities.destroy', props.university.id), {
            preserveScroll: true,
            // Redirect happens automatically on success
        });
    }
};

const intakeOptions = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];
</script>

<template>
    <Head :title="'Edit University: ' + university.name" />

    <AdminLayout>
        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <section>
                <header class="mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Edit University: {{ university.name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Update the university's details.
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
                        <InputLabel for="logo" value="Logo (Optional, Max 2MB, Upload to Replace)" />
                        <div class="mt-2 flex items-center space-x-4">
                            <img v-if="logoPreviewUrl" :src="logoPreviewUrl" class="h-16 w-16 object-cover rounded" alt="New Logo Preview">
                            <img v-else-if="existingLogoUrl" :src="existingLogoUrl" class="h-16 w-16 object-cover rounded" alt="Current Logo">
                            <span v-else class="text-sm text-gray-500">No logo uploaded.</span>
                        </div>
                        <input
                            id="logo"
                            type="file"
                            class="mt-2 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
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
                                    :checked="form.intake_months.includes(month)"
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

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Update University' }}
                            </PrimaryButton>
                             <Link
                                :href="route('admin.universities.index')"
                                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Cancel
                            </Link>
                        </div>
                        <DangerButton @click="deleteUniversity" type="button">
                            Delete University
                        </DangerButton>
                    </div>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>