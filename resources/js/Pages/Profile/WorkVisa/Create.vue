<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Or your ProfileLayout
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextareaInput from '@/Components/TextareaInput.vue'; // Assuming you have this
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    countries: Array,
    jobCategories: Array,
});

const form = useForm({
    destination_country_id: '',
    job_category_id: '',
    // job_posting_id: '', // Add if needed
    user_notes: '',
});

const submit = () => {
    form.post(route('api.work-visa-applications.store'), { // Use API route
        onSuccess: () => {
             form.reset();
             // Redirect to index page after successful submission
             // router.visit(route('profile.work-visa.index'));
             alert('Application submitted successfully!'); // Simple feedback
        },
        onError: (errors) => {
            console.error("Error submitting application:", errors);
            alert('Failed to submit application. Please check the form.'); // Simple feedback
        }
    });
};
</script>

<template>
    <Head title="Apply for Work Visa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Apply for Work Visa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                         <form @submit.prevent="submit">
                            <div class="mt-4">
                                <InputLabel for="destination_country_id" value="Destination Country" />
                                <select id="destination_country_id" v-model="form.destination_country_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled>Select a country</option>
                                    <option v-for="country in countries" :key="country.id" :value="country.id">
                                        {{ country.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.destination_country_id" />
                            </div>

                             <div class="mt-4">
                                <InputLabel for="job_category_id" value="Job Category (Optional)" />
                                <select id="job_category_id" v-model="form.job_category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select a category</option>
                                    <option v-for="category in jobCategories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.job_category_id" />
                            </div>

                             <div class="mt-4">
                                <InputLabel for="user_notes" value="Notes (Optional)" />
                                <TextareaInput id="user_notes" class="mt-1 block w-full" v-model="form.user_notes" rows="4" />
                                <InputError class="mt-2" :message="form.errors.user_notes" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('profile.work-visa.index')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </Link>

                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Submit Application
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>